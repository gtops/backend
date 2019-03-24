import { first } from "lodash";
import { errors } from "../../api-errors";
import { client } from "../../core/Database";
import { IParticipantResult } from "./IParticipantResult";

export class ParticipantServices {
	public async getDataParticipant(uid: string): Promise<IParticipantResult[]> {
		let query = `SELECT participant_id FROM participant WHERE uid = '${uid}'`;
		let result = await client.query(query);
		if (result.rows.length === 0) {
			throw errors.NotFoundParticipantUid;
		}
		const participantId = first(result.rows).participant_id;

		// tslint:disable:max-line-length
		query = `
		SELECT
			competition.date_of_competition,
		    trial.name_of_trial,
		    result_participant_on_trial.primary_result,
		    result_participant_on_trial.secondary_result,
		    result_participant_on_trial.unique_number
		FROM participant
		LEFT JOIN participant_on_competition ON participant_on_competition.participant_id = participant.participant_id
		LEFT JOIN competition ON competition.competition_id = participant_on_competition.competition_id
		LEFT JOIN result_participant_on_trial ON result_participant_on_trial.participant_on_competition_id = participant_on_competition.participant_on_competition_id
		LEFT JOIN trial ON trial.trial_id = result_participant_on_trial.trial_id
		LEFT JOIN final_result_participant_on_competition ON final_result_participant_on_competition.participant_on_competition_id = participant_on_competition.participant_on_competition_id
		WHERE participant.participant_id = '${participantId}';
		`;
		// tslint:enable:max-line-length

		result = await client.query(query);
		const participantResult: IParticipantResult[] = result.rows;

		return Promise.all(participantResult.map((item: IParticipantResult) => {
			return item.secondary_result === null ? this.calculateSecondaryResult(item) : item;
		}));
	}

	private async calculateSecondaryResult(item: IParticipantResult): Promise<IParticipantResult> {
		const query = ``;
		await client.query(query);
		// TODO: сделать расчет вторичного результата

		item.secondary_result = 100;

		return item;
	}
}
