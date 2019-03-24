export interface IParticipantResult {
	participant_on_competition_id: number;
	date_of_competition: Date;
	trial_id: number;
	gender_id: number;
	age_category_id: number;
	name_of_trial: string;
	primary_result: number;
	secondary_result: number | null;
	unique_number: string;
}
