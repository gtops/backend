import { errors } from "@api-errors/ApiError";
import { IRequest } from "@core/routes/interfaces/IRequest";
import { autobind } from "core-decorators";
import { Next, Response } from "restify";
import { ParticipantServices } from "../services";
import { Tool } from "../tools/Tool";

@autobind
export class ParticipantController {
	private services = new ParticipantServices();

	public async getDataParticipant(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			const { uid } = request.params;
			if (!Tool.isUid(uid)) {
				throw errors.IncorrectUid;
			}

			await this.services.getDataParticipant(uid)
				.then((data) => {
					response.send(data);
				});
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}

	public async getListAgeCategories(request: IRequest, response: Response, next: Next): Promise<void> {
		try {
			await this.services.getListAgeCategories()
				.then((data) => {
					response.send(data);
				});
		} catch (error) {
			if (!error.status) {
				error = errors.ServerError;
			}
			response.send(error);
		}
		next();
	}
}
