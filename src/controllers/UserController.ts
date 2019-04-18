import { Next, Response } from "restify";
import { errors } from "../api-errors";
import { IRequest } from "../core/routes/interfaces/IRequest";
import { UserServices } from "../services/UserServices";

export class UserController {
	private services = new UserServices();

	public async getAllRoles(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			const result = await this.services.getAllRoles();
			response.send(result);
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}
}
