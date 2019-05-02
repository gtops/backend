import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("trial_on_group"))
export class TrialOnGroup extends Model<TrialOnGroup> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public trial_on_group_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public group_in_standard_parent_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.DOUBLE)
	public result_for_gold: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.DOUBLE)
	public result_for_silver: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.DOUBLE)
	public result_for_bronze: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.BOOLEAN)
	public is_main_trial: boolean;
}
