import * as jwt from "jsonwebtoken";
import { head } from "lodash";
import * as md5 from "md5";
import { errors } from "../api-errors";
import { Config } from "../config";
import { client } from "../core/Database";
import { ILoginParams } from "../interfaces/authorization/ILoginParams";
import { IUserData } from "../interfaces/authorization/IUserData";
import { IUserRegistrationParams } from "../interfaces/authorization/IUserRegistrationParams";

export class AuthorizationServices {
	public async login(params: ILoginParams): Promise<string> {
		const query = `
			SELECT "user".user_id, "user".password, role.name_of_role FROM "user"
			LEFT JOIN role ON role.role_id = "user".role_id
			WHERE "user".login = '${params.login}'`;
		const result = await client.query(query);
		const userData: IUserData = head(result.rows);

		if (!userData) {
			throw errors.UserNotFound;
		}
		if (md5(params.password) !== userData.password) {
			throw errors.IncorrectPassword;
		}

		const tokenData = {
			user_id: userData.user_id,
			role: userData.name_of_role
		};
		const secret = Config.jwt.secret;
		const options = { expiresIn: Config.jwt.tokenTimeLive };

		return jwt.sign(tokenData, secret, options);
	}

	public async registration(params: IUserRegistrationParams): Promise<void> {
		let query = `
			SELECT "user".user_id FROM "user"
			WHERE "user".login = '${params.login}' OR "user".email = '${params.email}'`;
		const result = await client.query(query);

		if (head(result.rows)) {
			throw errors.UserAlreadyExist;
		}

		query = `
			INSERT INTO "user"(login, password, role_id, email, created_at, updated_at) 
			VALUES ('${params.login}', '${md5(params.password)}', ${params.role}, '${params.email}', NOW(), NOW())`;
		await client.query(query);
	}
}
