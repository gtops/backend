import {
	AllowNull,
	BelongsTo,
	Column,
	DataType,
	ForeignKey, HasMany,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";
import { StandardParent } from "../standardParent/StandardParent";
import { getOptions } from "../tools/options";
import { TrialOnGroup } from "../trialOnGroup/TrialOnGroup";

@Table(getOptions("group_in_standard_parent"))
export class GroupInStandardParent extends Model<GroupInStandardParent> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public group_in_standard_parent_id: number;

	@ForeignKey(() => StandardParent)
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public standard_parent_id: number;

	@BelongsTo(() => StandardParent)
	public standardParent: StandardParent;

	@HasMany(() => TrialOnGroup)
	public trialOnGroup: TrialOnGroup[];
}
