import * as jwt from "jsonwebtoken";
import { head } from "lodash";
import * as md5 from "md5";
import * as uuidv4 from "uuid/v4";
import { errors } from "../api-errors";
import { Config } from "../config";
import { client } from "../core/Database";
import { ILoginParams } from "../interfaces/authorization/ILoginParams";
import { IUserData } from "../interfaces/authorization/IUserData";
import { IUserRegistrationParams } from "../interfaces/authorization/IUserRegistrationParams";
import { ERoles } from "../middleware/interfaces/ERoles";
import { EmailServices } from "./EmailServices";

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

	public async registration(data: IUserRegistrationParams, roleId: number): Promise<void> {
		let query = `
			SELECT "user".user_id FROM "user"
			WHERE "user".login = '${data.login}' OR "user".email = '${data.email}'`;
		const result = await client.query(query);

		if (head(result.rows)) {
			throw errors.UserAlreadyExist;
		}

		query = `
			INSERT INTO "user"(login, password, role_id, email, created_at, updated_at) 
			VALUES ('${data.login}', '${md5(data.password)}', ${roleId}, '${data.email}', NOW(), NOW())`;
		await client.query(query);
	}

	public async invite(email: string, roleId: number): Promise<void> {
		const query = `
			SELECT "user".user_id FROM "user"
			WHERE "user".email = '${email}'`;
		const result = await client.query(query);
		if (result.rows.length !== 0) {
			throw errors.UserAlreadyExist;
		}
		const secret = Config.jwtInviteLink.secret;
		const options = { expiresIn: Config.jwtInviteLink.tokenTimeLive };
		const tokenData = { email, role: ERoles.INVITED_USER, role_id: roleId };
		const url = `${Config.addressFrontendServer.url}/user/invite?invite_token=${jwt.sign(tokenData, secret, options)}`;

		await EmailServices.send({
			from: Config.email.addressFrom,
			to: email,
			subject: `Приглашение на создание аккаунта в ${Config.projectName}`,
			html: `<div>
                        <p>Вам было выслано приглашение на создание аккаунта в ${Config.projectName}</p>
                        <a href="${url}">Ссылка для регистрации</a>
                   </div>`
		});
	}
}
