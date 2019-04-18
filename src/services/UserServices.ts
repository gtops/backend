import { client } from "../core/Database";
import { IRole } from "../interfaces/user/IRole";

export class UserServices {
	public async getAllRoles(): Promise<IRole[]> {
		const query = `
			SELECT role_id, name_of_role FROM role`;
		const result = await client.query(query);
		return result.rows;
	}
}
