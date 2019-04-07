import { head } from "lodash";
import { client } from "../core/Database";
import { ICalculateParams } from "../interfaces/calculation/ICalculateParams";
import { ICalculateResult } from "../interfaces/calculation/ICalculateResult";

export class CalculationServices {
	public async calculate(params: ICalculateParams): Promise<ICalculateResult> {
		const query = `
		SELECT *
		FROM (
			WITH pivoted_array AS (
				SELECT UNNEST(results) AS result,
					   result_for_gold,
			           result_for_silver,
			           result_for_bronze
			    FROM result_guide
			    WHERE result_guide.trial_id = ${params.trial_id}
			        AND result_guide.gender_id = ${params.gender_id}
			        AND result_guide.age_category_id = ${params.age_category_id}
			    ORDER BY result DESC)
			SELECT ROW_NUMBER() OVER() AS secondary_result, 
				   result AS primary_result,
				   result_for_gold,
		           result_for_silver,
		           result_for_bronze
			FROM pivoted_array
		) AS result
		WHERE result.primary_result <= ${params.primary_result} LIMIT 1`;
		const res = await client.query(query);
		return head(res.rows) ? head(res.rows) : {};
	}
}
