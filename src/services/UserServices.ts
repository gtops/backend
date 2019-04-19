import { head } from "lodash";
import { client } from "../core/Database";
import { IRole } from "../interfaces/user/IRole";
import { IUser } from "../interfaces/user/IUser";

export class UserServices {
	public async getAllRoles(): Promise<IRole[]> {
		const query = `
			SELECT role_id, name_of_role FROM role`;
		const result = await client.query(query);
		return result.rows;
	}

	public async getUserInfo(userId: number): Promise<IUser> {
		const query = `
			SELECT user_id, email, name_of_role FROM "user"
			LEFT JOIN role ON role.role_id = "user".role_id
			WHERE user_id = ${userId}`;
		const result = await client.query(query);
		return head(result.rows);
	}
}
