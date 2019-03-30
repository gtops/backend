import { autobind } from "core-decorators";
import { Next, Response } from "restify";
import { errors } from "../api-errors";
import { IRequest } from "../core/routes/interfaces/IRequest";
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
					console.log(uid);
					response.send({ message: data });
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
