import * as jwt from "jsonwebtoken";
import * as md5 from "md5";
import { errors } from "../api-errors";
import { config } from "../config/Config";
import { ILoginParams } from "../interfaces/authorization/ILoginParams";
import { IUserRegistrationParams } from "../interfaces/authorization/IUserRegistrationParams";
import { ERoles } from "../middleware/interfaces/ERoles";
import { Role } from "../models/role/Role";
import { User } from "../models/user/User";
import { EmailServices } from "./EmailServices";

export class AuthorizationServices {
	public async login(params: ILoginParams): Promise<string> {
		const userData = await User.findOne({
			include: [Role],
			where: { login: params.login }
		});

		if (!userData) {
			throw errors.UserNotFound;
		}
		if (md5(params.password) !== userData.password) {
			throw errors.IncorrectPassword;
		}

		const tokenData = {
			user_id: userData.user_id,
			role: userData.role.name_of_role
		};
		const secret = config.jwt.secret;
		const options = { expiresIn: config.jwt.tokenTimeLive };

		return jwt.sign(tokenData, secret, options);
	}

	public async registration(data: IUserRegistrationParams, roleId: number, email: string): Promise<void> {
		const user = await User.findOne({ where: { email } });
		if (user !== null) {
			throw errors.UserAlreadyExist;
		}

		await User.create({
			login: data.login,
			password: md5(data.password),
			role_id: roleId,
			email
		});
	}

	public async invite(email: string, roleId: number): Promise<void> {
		const user = await User.findOne({ where: { email } });
		if (user !== null) {
			throw errors.UserAlreadyExist;
		}

		const secret = config.jwtInviteLink.secret;
		const options = { expiresIn: config.jwtInviteLink.tokenTimeLive };
		const tokenData = { email, role: ERoles.INVITED_USER, role_id: roleId };
		const url = `${config.addressFrontendServer.url}/user/invite?invite_token=${jwt.sign(tokenData, secret, options)}`;

		await EmailServices.send({
			from: config.email.addressFrom,
			to: email,
			subject: `Приглашение на создание аккаунта в ${config.projectName}`,
			html: `<div>
                        <p>Вам было выслано приглашение на создание аккаунта в ${config.projectName}</p>
                        <a href="${url}">Ссылка для регистрации</a>
                   </div>`
		});
	}
}
