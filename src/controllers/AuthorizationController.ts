import { errors } from "@api-errors/ApiError";
import { IRequest } from "@core/routes/interfaces/IRequest";
import { autobind } from "core-decorators";
import { Next, Response } from "restify";
import { AuthorizationServices } from "../services";

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
			const data = request.body;
			const roleId = request.role_id;
			const email = request.email;
			await this.services.registration(data, roleId, email);
			response.send({ message: "Регистрация успешно пройдена" });
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}

	public async invite(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			const { email, role_id } = request.body;
			await this.services.invite(email, role_id);
			response.send({ message: `Приглашение успешно отправлено на почту: ${email}` });
		} catch (error) {
			console.log(error);
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}

	public async getRegistrationEmail(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			const email = request.email;
			response.send({ email });
		} catch (error) {
			console.log(error);
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}
}
