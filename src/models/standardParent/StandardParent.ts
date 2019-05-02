import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("standard_parent"))
export class StandardParent extends Model<StandardParent> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public standard_parent_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public gender_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public age_category_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public count_trial_for_gold: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public count_trial_for_silver: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public count_trial_for_bronze: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public count_all_trials: number;
}
