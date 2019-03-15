import { client } from "../core/Database";

export class ParticipantServices {
	public async getDataParticipant(uid: string): Promise<any> {
		const result = await client.query("SELECT 1 + 1");
		console.log(result.rows);

		return "It's work!";
	}
}
