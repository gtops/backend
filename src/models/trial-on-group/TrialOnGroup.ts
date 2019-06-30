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
import { GroupInStandardParent } from "../group-in-standard-parent/GroupInStandardParent";
import { getOptions } from "../tools/options";
import { Trial } from "../trial/Trial";

@Table(getOptions("trial_on_group"))
export class TrialOnGroup extends Model<TrialOnGroup> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public trial_on_group_id: number;

	@ForeignKey(() => GroupInStandardParent)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public group_in_standard_parent_id: number;

	@ForeignKey(() => Trial)
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

	@BelongsTo(() => GroupInStandardParent)
	public groupInStandardParent: GroupInStandardParent;

	@BelongsTo(() => Trial)
	public trial: Trial;
}
