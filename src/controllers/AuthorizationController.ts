import { autobind } from "core-decorators";
import { Next, Request, Response } from "restify";
import { AuthorizationServices } from "../services";

@autobind
export class AuthorizationController {
	private services = new AuthorizationServices();

	public async login(request: Request, response: Response, next: Next): Promise<void> {
		try {
			const result = await this.services.login(request.body);
			response.send({ token: result });
		} catch (error) {
			response.send(error);
		}
		next();
	}

	public async registration(request: Request, response: Response, next: Next): Promise<void> {
		try {
			await this.services.registration(request.body);
			response.send({ message: "Регистрация успешно пройдена" });
		} catch (error) {
			response.send(error);
		}
		next();
	}
}
