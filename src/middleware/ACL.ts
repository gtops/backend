import { autobind } from "core-decorators";
import * as jwt from "jsonwebtoken";
import { indexOf } from "lodash";
import { Next, Request, Response } from "restify";
import { errors } from "../api-errors";
import { Config } from "../config";
import { IRequest } from "../core/routes/interfaces/IRequest";
import { EMethod } from "../core/routes/interfaces/IRouteDefinition";
import { ERoles } from "./interfaces/ERoles";
import { IRolesStore } from "./interfaces/IRolesStore";
import { IUser } from "./interfaces/IUser";

@autobind
export class ACL {
	private static readonly authorizationTokenName = "token";
	private rolesStore: IRolesStore[] = [];

	public static getToken(request: Request): string {
		return request.header(ACL.authorizationTokenName);
	}

	public allow(roles: ERoles[], path: string, method: EMethod): void {
		const key = `${method}_${path}`;
		this.rolesStore[key] = roles;
	}

	public async auth(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			const key = `${request.getRoute().method}_${request.getRoute().path}`;
			const allowedRoles = this.rolesStore[key];

			if (!allowedRoles) {
				return next();
			}

			const token = ACL.getToken(request);
			if (!token) {
				if (indexOf(allowedRoles, ERoles.GUEST) === -1) {
					throw errors.PermissionError;
				}
				request.role = ERoles.GUEST;
			} else {
				const userInfo = ACL.getTokenInfo(token);
				if (indexOf(allowedRoles, userInfo.role) === -1) {
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

	private static getTokenInfo(token: string): IUser {
		try {
			const secret = Config.jwt.secret;
			return  jwt.verify(token, secret) as IUser;
		} catch (e) {
			throw errors.InvalidToken;
		}
	}
}
