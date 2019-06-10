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
import { Position } from "../Position/Position";
import { RefereeOnTrialInCompetition } from "../RefereeOnTrialInCompetition/RefereeOnTrialInCompetition";
import { Role } from "../role/Role";
import { getOptions } from "../tools/options";
import { User } from "../user/User";
import { WorkerOfUser } from "../workerOfUser/WorkerOfUser";

@Table(getOptions("worker_of_user_in_competition"))
export class WorkerOfUserInCompetition extends Model<WorkerOfUserInCompetition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public worker_of_user_in_competition_id: number;

	@ForeignKey(() => WorkerOfUser)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public worker_of_user_id: number;

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

	@ForeignKey(() => Competition)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public competition_id: number;

	@BelongsTo(() => Position)
	public position: Position;

	@BelongsTo(() => Competition)
	public competition: Competition;

	@BelongsTo(() => Role)
	public role: Role;

	@HasMany(() => RefereeOnTrialInCompetition)
	public referee: RefereeOnTrialInCompetition[];
}
