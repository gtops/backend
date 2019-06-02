import { AllowNull, Column, DataType, HasMany, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { ResultGuide } from "../resultGuide/ResultGuide";
import { StandardParent } from "../standardParent/StandardParent";
import { getOptions } from "../tools/options";

@Table(getOptions("age_category"))
export class AgeCategory extends Model<AgeCategory> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public age_category_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public min_age: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public max_age: number;

	@HasMany(() => ResultGuide)
	public resultGuide: ResultGuide[];

	@HasMany(() => StandardParent)
	public standardParent: StandardParent[];
}
