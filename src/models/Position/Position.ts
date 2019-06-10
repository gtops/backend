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
import { ParticipantOnCompetition } from "../participantOnCompetition/ParticipantOnCompetition";
import { getOptions } from "../tools/options";
import { WorkerOfUser } from "../workerOfUser/WorkerOfUser";
import { WorkerOfUserInCompetition } from "../WorkerOfUserInCompetition/WorkerOfUserInCompetition";

@Table(getOptions("position"))
export class Position extends Model<Position> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public position_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public name: string;

	@HasMany(() => WorkerOfUser)
	public workerOfUser: WorkerOfUser[];

	@HasMany(() => WorkerOfUserInCompetition)
	public workerOfUserInCompetition: WorkerOfUserInCompetition[];
}
