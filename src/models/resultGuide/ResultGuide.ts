import {
	AllowNull,
	BelongsTo,
	Column,
	DataType,
	ForeignKey,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";
import { AgeCategory } from "../ageCategory/AgeCategory";
import { Gender } from "../gender/Gender";
import { getOptions } from "../tools/options";
import { Trial } from "../trial/Trial";
import { User } from "../user/User";

@Table(getOptions("result_guide"))
export class ResultGuide extends Model<ResultGuide> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public result_guide_id: number;

	@ForeignKey(() => Trial)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@ForeignKey(() => Gender)
	@Column(DataType.INTEGER)
	public gender_id: number;

	@ForeignKey(() => AgeCategory)
	@Column(DataType.INTEGER)
	public age_category_id: number;

	@ForeignKey(() => User)
	@Column(DataType.INTEGER)
	public user_id: number;

	@Column(DataType.INTEGER)
	public version: number;

	@Column(DataType.BOOLEAN)
	public is_primary_guide: boolean;

	@Column({
		type: DataType.STRING
	})
	public results: number[];

	@BelongsTo(() => Trial)
	public trial: Trial;

	@BelongsTo(() => Gender)
	public gender: Gender;

	@BelongsTo(() => AgeCategory)
	public ageCategory: AgeCategory;

	@BelongsTo(() => User)
	public user: User;
}
