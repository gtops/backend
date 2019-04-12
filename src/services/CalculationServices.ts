import { head } from "lodash";
import { client } from "../core/Database";
import { ICalculateParams } from "../interfaces/calculation/ICalculateParams";
import { ICalculateResult } from "../interfaces/calculation/ICalculateResult";
import { IParticipantParams } from "../interfaces/calculation/IParticipantParams";
import { IParticipantTrial } from "../interfaces/calculation/IParticipantTrial";

export class CalculationServices {
	public async getParticipantTrial(params: IParticipantParams): Promise<IParticipantTrial[]> {
		let query = `
		SELECT age_category_id FROM age_category WHERE 
		`;

		const ageCategoryId = 1;
		query = `
		SELECT result_guide.trial_id,
		       trial.name_of_trial,
		       result_guide.result_for_gold,
		       result_guide.result_for_silver,
		       result_guide.result_for_bronze,
		       result_guide.is_main_trial,
		       result_guide.result_guide_id
		FROM result_guide
		       LEFT JOIN trial ON result_guide.trial_id = trial.trial_id
		WHERE result_guide.age_category_id = ${ageCategoryId}
		  AND result_guide.gender_id = ${params.gender_id}
		ORDER BY result_guide.is_main_trial DESC`;
		const result = await client.query(query);
		return head(result.rows);
	}

	public async calculate(params: ICalculateParams, isResultRequired: boolean): Promise<ICalculateResult> {
		const results = `result_for_gold, result_for_silver, result_for_bronze`;
		const query = `
		SELECT *
		FROM (
			WITH pivoted_array AS (
				SELECT UNNEST(results) AS result, ${isResultRequired ? results : ""}
			    FROM result_guide
			    WHERE result_guide.trial_id = ${params.trial_id}
			        AND result_guide.gender_id = ${params.gender_id}
			        AND result_guide.age_category_id = ${params.age_category_id}
			    ORDER BY result DESC)
			SELECT ROW_NUMBER() OVER() AS secondary_result, 
				   result AS primary_result,
				   ${isResultRequired ? results : ""}
			FROM pivoted_array
		) AS result
		WHERE result.primary_result <= ${params.primary_result} LIMIT 1`;
		const result = await client.query(query);
		return head(result.rows) ? head(result.rows) : {};
	}
}
