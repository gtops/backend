import { Role } from "@models/role/Role";
import { User } from "@models/user/User";
import { IRole } from "../interfaces/user/IRole";
import { errors } from "@api-errors/ApiError";

export class UserServices {
	public async getAllRoles(): Promise<IRole[]> {
		return Role.findAll();
	}

	public async getUserInfo(userId: number): Promise<User> {
		const user = await User.findByPk(userId);
		if (user == null) { throw errors.UserNotFound; }
		return user;
	}
}
