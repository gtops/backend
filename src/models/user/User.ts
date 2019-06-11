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

import { Competition } from "../competition/Competition";
import { ResultGuide } from "../result-guide/ResultGuide";
import { Role } from "../role/Role";
import { getOptions } from "../tools/options";
import { WorkerOfUser } from "../worker-of-user/WorkerOfUser";

@Table(getOptions("user"))
export class User extends Model<User> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
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

	@ForeignKey(() => Role)
	@Column(DataType.INTEGER)
	public role_id: number;

	@BelongsTo(() => Role)
	public role: Role;

	@HasMany(() => Competition)
	public competition: Competition[];

	@HasMany(() => WorkerOfUser)
	public workerOfUser: WorkerOfUser[];

	@HasMany(() => ResultGuide)
	public resultGuide: ResultGuide[];
}
