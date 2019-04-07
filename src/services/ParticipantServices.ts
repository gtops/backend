import { head } from "lodash";
import { errors } from "../api-errors";
import { client } from "../core/Database";
import { IFinalResult } from "../interfaces/participant/IFinalResult";
import { IParticipantResult } from "../interfaces/participant/IParticipantResult";
import { CalculationServices } from "./CalculationServices";
import { ICalculateResult } from "../interfaces/calculation/ICalculateResult";

export class ParticipantServices {
	private services = new CalculationServices();

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
		    result_participant_on_trial.result_participant_on_trial_id,
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
		WHERE participant.participant_id = ${participantId};
		`;
		// tslint:enable:max-line-length

		result = await client.query(query);
		const participantResult: IParticipantResult[] = result.rows;

		return Promise.all(participantResult.map((item: IParticipantResult) => {
			return item.secondary_result === null ? this.calculateSecondaryResult(item) : item;
		}));
	}

	private async calculateSecondaryResult(item: IParticipantResult): Promise<IParticipantResult> {
		const finalResult: ICalculateResult = await this.services.calculate(item);

		item.secondary_result = !finalResult.secondary_result ? 0 :
			await this.updateParticipantSecondaryResult(finalResult.secondary_result, item.result_participant_on_trial_id);

		return item;
	}

	private async updateParticipantSecondaryResult(
		secondaryResult: number,
		resultParticipantOnTrialId: number
	): Promise<number> {
		const query = `
		UPDATE result_participant_on_trial
		SET secondary_result = ${secondaryResult}
		WHERE result_participant_on_trial_id = ${resultParticipantOnTrialId}`;
		await client.query(query);

		return secondaryResult;
	}
}
