import { Next, Response } from "restify";
import { errors } from "../api-errors";
import { IRequest } from "../core/routes/interfaces/IRequest";
import { CalculationServices } from "../services";

export class CalculationController {
	private services = new CalculationServices();

	public async getParticipantTrial(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			/*const options = {
				from: "fedok251@gmail.com",
				to: "rybakov.f@mail.ru",
				subject: "Hello ✔",
				text: "Hello world?",
			};
			const r = await EmailServices.send(options);
			console.log(r);*/
			await this.services.getParticipantTrial(request.body)
				.then((result) => {
					response.send(result);
				});
		} catch (error) {
			console.log(error);
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		return next();
	}

	public async calculate(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			await this.services.calculate(request.body)
				.then((result) => {
					response.send(result);
				});
		} catch (error) {
			console.log(error);
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		return next();
	}
}
