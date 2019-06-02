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
import { StandardParent } from "../standardParent/StandardParent";
import { getOptions } from "../tools/options";

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
}
