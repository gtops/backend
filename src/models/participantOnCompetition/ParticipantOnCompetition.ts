import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("participant_on_competition"))
export class ParticipantOnCompetition extends Model<ParticipantOnCompetition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public participant_on_competition_id: number;

	@Column(DataType.INTEGER)
	public participant_id: number;

	@Column(DataType.INTEGER)
	public competition_id: number;

	@Column(DataType.INTEGER)
	public age_category_id: number;
}
