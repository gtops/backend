import { autobind } from "core-decorators";
import { Next, Request, Response } from "restify";
import { ParticipantServices } from "../services";

@autobind
export class ParticipantController {
	private services = new ParticipantServices();

	public async getDataParticipant(request: Request, response: Response, next: Next) {
		try {
			const { uid } = request.params;
			console.log(uid);
			await this.services.getDataParticipant()
				.then((data) => {
					response.send({message: data});
				});
		} catch (error) {
			response.send(error);
			return next();
		}
	}
}
