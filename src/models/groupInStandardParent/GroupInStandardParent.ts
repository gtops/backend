import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("group_in_standard_parent"))
export class GroupInStandardParent extends Model<GroupInStandardParent> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public group_in_standard_parent_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public standard_parent_id: number;
}
