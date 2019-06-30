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
import { getOptions } from "../tools/options";
import { TrialOnCompetition } from "../trial-on-competition/TrialOnCompetition";
import { WorkerOfUserInCompetition } from "../worker-of-user-in-competition/WorkerOfUserInCompetition";

@Table(getOptions("referee_on_trial_in_competition"))
export class RefereeOnTrialInCompetition extends Model<RefereeOnTrialInCompetition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public referee_on_trial_in_competition_id: number;

	@ForeignKey(() => TrialOnCompetition)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@ForeignKey(() => WorkerOfUserInCompetition)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public worker_of_user_in_competition_id: number;

	@BelongsTo(() => TrialOnCompetition)
	public trialOnCompetition: TrialOnCompetition;

	@BelongsTo(() => WorkerOfUserInCompetition)
	public worker: WorkerOfUserInCompetition;
}
