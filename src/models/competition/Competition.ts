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
import { Role } from "../role/Role";
import { getOptions } from "../tools/options";
import { TrialOnCompetition } from "../trial-on-competition/TrialOnCompetition";
import { User } from "../user/User";
import { WorkerOfUserInCompetition } from "../WorkerOfUserInCompetition/WorkerOfUserInCompetition";

@Table(getOptions("competition"))
export class Competition extends Model<Competition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public competition_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column
	public date_of_start_competition: Date;

	@AllowNull(false)
	@Unique(true)
	@Column
	public date_of_end_competition: Date;

	@ForeignKey(() => User)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public user_id: number;

	@BelongsTo(() => User)
	public user: User;

	@Column(DataType.STRING(50))
	public name_of_competition: string;

	@HasMany(() => TrialOnCompetition)
	public trialOnCompetition: TrialOnCompetition[];

	@HasMany(() => WorkerOfUserInCompetition)
	public workerOfUserInCompetition: WorkerOfUserInCompetition[];
}
