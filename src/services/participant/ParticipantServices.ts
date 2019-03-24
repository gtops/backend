import { head } from "lodash";
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
		const participantId = head(result.rows).participant_id;

		// tslint:disable:max-line-length
		query = `
		SELECT
			competition.date_of_competition,
		    trial.name_of_trial,
		    trial.trial_id,
		    result_participant_on_trial.primary_result,
		    result_participant_on_trial.secondary_result,
		    result_participant_on_trial.unique_number,
		    participant.gender_id,
		    participant_on_competition.participant_on_competition_id,
		    age_category.age_category_id
		FROM participant
		LEFT JOIN participant_on_competition ON participant_on_competition.participant_id = participant.participant_id
		LEFT JOIN age_category ON participant_on_competition.age_category_id = age_category.age_category_id
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
		const query = `
		SELECT secondary_result 
		FROM type_of_trial_with_age_category
		WHERE type_of_trial_with_age_category.trial_id = '${item.trial_id}' AND
		      type_of_trial_with_age_category.gender_id = '${item.gender_id}' AND
		      type_of_trial_with_age_category.age_category_id = '${item.age_category_id}' AND
		      type_of_trial_with_age_category.primary_result = '${item.primary_result}'`;
		const res = await client.query(query);
		item.secondary_result = res.rows.length === 0 ? 0 :
			await this.updateParticipantSecondaryResult(head(res.rows).secondary_result, item.participant_on_competition_id);

		return item;
	}

	private async updateParticipantSecondaryResult(
		secondaryResult: number,
		participantOnCompetitionId: number
	): Promise<number> {
		const query = `
		UPDATE result_participant_on_trial
		SET result_participant_on_trial.secondary_result = '${secondaryResult}'
		WHERE result_participant_on_trial.participant_on_competition_id = '${participantOnCompetitionId}'`;
		await client.query(query);

		return secondaryResult;
	}
}
