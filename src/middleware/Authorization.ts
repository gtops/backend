import { Next, Request, Response } from "restify";
import { ApiError } from "../api-errors/ApiError";
import { ACL } from "./ACL";
import { IUser } from "./interfaces/IUser";

/*
const acl: ACL = new ACL();

export const AuthorizationMiddleware = (req: Request, res: Response, next: Next) => {
	acl.auth(req).then((authResult: IUser) => {
		req.user = authResult.user;
		next();
	}, (error: ApiError) => {
		res.send(error);
		next(error);
	});
};
*/
