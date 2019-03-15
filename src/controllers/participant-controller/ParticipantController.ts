import { autobind } from "core-decorators";
import { Next, Request, Response } from "restify";
import { errors } from "../../api-errors";
import { ParticipantServices } from "../../services";
import { Tool } from "../../tools/Tool";

@autobind
export class ParticipantController {
	private services = new ParticipantServices();

	public async getDataParticipant(request: Request, response: Response, next: Next): Promise<void> {
		try {
			const { uid } = request.params;
			if (!Tool.isUid(uid)) {
				throw errors.IncorrectUid;
			}

			await this.services.getDataParticipant(uid)
				.then((data) => {
					response.send({ message: data });
				});
		} catch (error) {
			response.send(error);
			return next();
		}
	}
}
