import { autobind } from "core-decorators";
import { Next, Response } from "restify";
import { IRequest } from "../core/routes/interfaces/IRequest";
import { AuthorizationServices } from "../services";
import { errors } from "../api-errors";

@autobind
export class AuthorizationController {
	private services = new AuthorizationServices();

	public async login(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			const result = await this.services.login(request.body);
			response.send({ token: result });
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}

	public async registration(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			await this.services.registration(request.body);
			response.send({ message: "Регистрация успешно пройдена" });
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}
}
