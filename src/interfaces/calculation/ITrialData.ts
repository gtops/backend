import { ITrialList } from "./ITrialList";

export interface ITrialData {
	age_category_id: number;
	count_all_trials: number;
	count_trial_for_gold: number;
	count_trial_for_silver: number;
	count_trial_for_bronze: number;
	trials: ITrialList[];
}
