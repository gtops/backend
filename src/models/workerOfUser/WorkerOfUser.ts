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
import { Position } from "../Position/Position";
import { Role } from "../role/Role";
import { getOptions } from "../tools/options";
import { User } from "../user/User";
import { WorkerOfUserInCompetition } from "../WorkerOfUserInCompetition/WorkerOfUserInCompetition";

@Table(getOptions("worker_of_user"))
export class WorkerOfUser extends Model<WorkerOfUser> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public worker_of_user_id: number;

	@ForeignKey(() => User)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public user_id: number;

	@ForeignKey(() => Position)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public position_id: number;

	@ForeignKey(() => Role)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public role_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public login: string;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public email: string;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public password: string;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public name: string;

	@BelongsTo(() => User)
	public user: User;

	@BelongsTo(() => Position)
	public position: Position;

	@BelongsTo(() => Role)
	public role: Role;

	@HasMany(() => WorkerOfUserInCompetition)
	public workerOfUserInCompetition: WorkerOfUserInCompetition[];
}
