import { AllowNull, BelongsTo, Column, DataType, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { getOptions } from "../tools/options";
import { Role } from "../role/Role";

@Table(getOptions("user"))
export class User extends Model<User> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public user_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public login: string;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public email: string;

	@Column(DataType.STRING(50))
	public password: string;

	@Column(DataType.INTEGER)
	public role_id: number;

	@BelongsTo(() => Role)
	public role: Role;
}
