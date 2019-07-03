import { User } from "@models/user/User";
import {
	AllowNull, BelongsTo,
	Column,
	DataType,
	ForeignKey,
	HasMany,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";
import { AgeCategory } from "../age-category/AgeCategory";
import { Gender } from "../gender/Gender";
import { GroupInStandardParent } from "../group-in-standard-parent/GroupInStandardParent";
import { getOptions } from "../tools/options";

@Table(getOptions("standard_parent"))
export class StandardParent extends Model<StandardParent> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public standard_parent_id: number;

	@ForeignKey(() => Gender)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public gender_id: number;

	@ForeignKey(() => AgeCategory)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public age_category_id: number;

	@ForeignKey(() => User)
	@Column(DataType.INTEGER)
	public user_id: number;

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

	@Column(DataType.INTEGER)
	public version: number;

	@HasMany(() => GroupInStandardParent)
	public groupInStandardParent: GroupInStandardParent[];

	@BelongsTo(() => Gender)
	public gender: Gender;

	@BelongsTo(() => AgeCategory)
	public ageCategory: AgeCategory;

	@BelongsTo(() => User)
	public user: User;
}
