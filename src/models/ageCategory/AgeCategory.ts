import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("age_category"))
export class AgeCategory extends Model<AgeCategory> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public age_category_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public min_age: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public max_age: number;
}
