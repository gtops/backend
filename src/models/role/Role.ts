import { AllowNull, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";

@Table(getOptions("role"))
export class Role extends Model<Role> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public role_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public name_of_role: string;
}
