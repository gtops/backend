import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("trial_on_competition"))
export class TrialOnCompetition extends Model<TrialOnCompetition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public trial_on_competition_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public competition_id: number;
}
