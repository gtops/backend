export interface IParticipantTrial {
	trial_id: number;
	name_of_trial: string;
	result_for_gold: number;
	result_for_silver: number;
	result_for_bronze: number;
	is_main_trial: boolean;
}
