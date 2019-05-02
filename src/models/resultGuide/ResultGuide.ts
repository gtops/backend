import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("result_guide"))
export class ResultGuide extends Model<ResultGuide> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public result_guide_id: number;

	@Column(DataType.INTEGER)
	public trial_id: number;

	@Column(DataType.INTEGER)
	public gender_id: number;

	@Column(DataType.INTEGER)
	public age_category_id: number;

	@Column(DataType.DOUBLE)
	public results: number[];
}
