import { client } from "@core/Database";
import { ResultGuide } from "@models/result-guide/ResultGuide";
import { findIndex, head } from "lodash";
import { ICalculateParams } from "../interfaces/calculation/ICalculateParams";
import { ICalculateResult } from "../interfaces/calculation/ICalculateResult";
import { IParticipantParams } from "../interfaces/calculation/IParticipantParams";
import { ITrialData } from "../interfaces/calculation/ITrialData";

export class CalculationServices {
	public async getParticipantTrial(params: IParticipantParams): Promise<ITrialData> {
		const trialsDataQuery = `
		SELECT age_category.age_category_id,
		       count_all_trials,
		       count_trial_for_gold,
		       count_trial_for_silver,
		       count_trial_for_bronze
		FROM standard_parent
		         LEFT JOIN age_category ON age_category.age_category_id = standard_parent.age_category_id
		WHERE ${this.getAgeSign} AND standard_parent.gender_id = ${params.gender_id}`;

		const trialsQuery = `
		SELECT array_agg(json_build_object(
				'trial_id', trial.trial_id,
		        'name_of_trial', trial.name_of_trial,
		        'result_for_gold', trial_in_group.result_for_gold,
		        'result_for_silver', trial_in_group.result_for_silver,
		        'result_for_bronze', trial_in_group.result_for_bronze,
		        'is_main_trial', trial_in_group.is_main_trial
		    )) as trial_group
		FROM trial
		         LEFT JOIN trial_in_group ON trial.trial_id = trial_in_group.trial_id
		         LEFT JOIN group_in_standard_parent
		              ON group_in_standard_parent.group_in_standard_parent_id = trial_in_group.group_in_standard_parent_id
		         LEFT JOIN standard_parent ON standard_parent.standard_parent_id = group_in_standard_parent.standard_parent_id
		         LEFT JOIN age_category ON age_category.age_category_id = standard_parent.age_category_id
		WHERE ${this.getAgeSign} AND standard_parent.gender_id = ${params.gender_id}
		GROUP BY trial_in_group.group_in_standard_parent_id`;

		const result = await Promise.all([client.query(trialsDataQuery), client.query(trialsQuery)]);

		return {
			...head(result[0].rows),
			trials: result[1].rows
		};
	}

	public async calculate(params: ICalculateParams): Promise<ICalculateResult> {
		const { trial_id, gender_id, age_category_id, primary_result } = params;
		return ResultGuide.findOne({
				where: {
					trial_id,
					gender_id,
					age_category_id,
				},
				order: "results DESC"
			})
			.then((resultGuideData) => {
				const index = findIndex(resultGuideData.results, (elem: number) => elem <= primary_result);
				return {
					secondary_result: index,
					primary_result: resultGuideData[index]
				};
			});
		/*const query = `
		SELECT *
		FROM (
			WITH pivoted_array AS (
				SELECT UNNEST(results) AS result
			    FROM result_guide
			    WHERE result_guide.trial_id = ${params.trial_id}
			        AND result_guide.gender_id = ${params.gender_id}
			        AND result_guide.age_category_id = ${params.age_category_id}
			    ORDER BY result DESC)
			SELECT ROW_NUMBER() OVER() AS secondary_result, 
				   result AS primary_result
			FROM pivoted_array
		) AS result
		WHERE result.primary_result <= ${params.primary_result} LIMIT 1`;
		const result = await client.query(query);
		return head(result.rows) ? head(result.rows) : {};*/
	}

	private getAgeSign(params: IParticipantParams): string {
		return params.age_sign.old ?
			`age_category.min_age <= ${params.age_sign.old} AND age_category.max_age >= ${params.age_sign.old}` :
			`standard_parent.age_category_id = ${params.age_sign.age_category_id}`;
	}
}
