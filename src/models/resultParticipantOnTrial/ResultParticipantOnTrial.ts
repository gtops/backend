import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("result_participant_on_trial"))
export class ResultParticipantOnTrial extends Model<ResultParticipantOnTrial> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public result_participant_on_trial_id: number;

	@Column(DataType.INTEGER)
	public participant_on_competition_id: number;

	@Column(DataType.INTEGER)
	public order_of_participation: number;

	@Column(DataType.INTEGER)
	public primary_result: number;

	@Column(DataType.INTEGER)
	public secondary_result: number;

	@Column(DataType.INTEGER)
	public trial_id: number;

	@Column(DataType.INTEGER)
	public unique_number: number;
}
