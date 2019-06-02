import {
	AllowNull,
	Column,
	DataType,
	HasMany,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";

import { getOptions } from "../tools/options";
import { User } from "../user/User";

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

	@HasMany(() => User)
	public user: User[];
}
