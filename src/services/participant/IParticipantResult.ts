export interface IParticipantResult {
	date_of_competition: Date;
	name_of_trial: string;
	primary_result: number;
	secondary_result: number | null;
	unique_number: string;
}
