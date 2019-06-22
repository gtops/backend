import { errors } from "@api-errors/ApiError";
import { config } from "@config/Config";
import { IRequest } from "@core/routes/interfaces/IRequest";
import { EMethod } from "@core/routes/interfaces/IRouteDefinition";
import { autobind } from "core-decorators";
import * as jwt from "jsonwebtoken";
import { Next, Request, Response } from "restify";
import { ERoles } from "./interfaces/ERoles";
import { IRolesStore } from "./interfaces/IRolesStore";

@autobind
export class ACL {
	public static readonly authorizationTokenName = "token";
	public static readonly inviteTokenName = "invite_token";
	private rolesStore: IRolesStore[] = [];

	public static getToken(request: Request, type: string): string {
		return request.header(type);
	}

	public allow(roles: ERoles[], path: string, method: EMethod): void {
		const key = `${method}_${path}`;
		this.rolesStore[key] = roles;
	}

	public async auth(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			let token;
			const key = `${request.getRoute().method}_${request.getRoute().path}`;
			const allowedRoles: ERoles[] = this.rolesStore[key];

			if (!allowedRoles) {
				return next();
			}

			if (allowedRoles.includes(ERoles.INVITED_USER)) {
				token = ACL.getToken(request, ACL.inviteTokenName);
				if (!token) {
					throw errors.TokenNotFound;
				}
				const userInfo = ACL.getTokenInfo(token, ACL.inviteTokenName);
				if (!allowedRoles.includes(userInfo.role)) {
					throw errors.PermissionError;
				}
				if (!userInfo.email || userInfo.role_id ) {
					throw errors.InvalidToken;
				}
				request.email = userInfo.email;
				request.role = userInfo.role;
				request.role_id = userInfo.role_id;
				return next();
			}

			token = ACL.getToken(request, ACL.authorizationTokenName);
			if (!token) {
				if (!allowedRoles.includes(ERoles.GUEST)) {
					throw errors.PermissionError;
				}
				request.role = ERoles.GUEST;
			} else {
				const userInfo = ACL.getTokenInfo(token, ACL.authorizationTokenName);
				if (!allowedRoles.includes(userInfo.role)) {
					throw errors.PermissionError;
				}
				request.user_id = userInfo.user_id;
				request.role = userInfo.role;
			}
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			return response.send(error);
		}
		return next();
	}

	private static getTokenInfo(token: string, type: string): IRequest {
		try {
			const secret = type === ACL.authorizationTokenName ? config.jwt.secret :
				type === ACL.inviteTokenName ? config.jwtInviteLink.secret : "";
			return jwt.verify(token, secret) as IRequest;
		} catch (err) {
			throw errors.InvalidToken;
		}
	}
}
