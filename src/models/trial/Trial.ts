import {
	AllowNull,
	BelongsTo,
	Column,
	DataType, ForeignKey,
	HasMany,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";
import { ResultGuide } from "../result-guide/ResultGuide";
import { ResultParticipantOnTrial } from "../result-participant-on-trial/ResultParticipantOnTrial";
import { getOptions } from "../tools/options";
import { TrialOnCompetition } from "../trial-on-competition/TrialOnCompetition";
import { TrialOnGroup } from "../trial-on-group/TrialOnGroup";
import { Unit } from "../unit/Unit";

@Table(getOptions("trial"))
export class Trial extends Model<Trial> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public name_of_trial: string;

	@ForeignKey(() => Unit)
	@AllowNull(true)
	@Unique(false)
	@Column(DataType.INTEGER)
	public unit_id: number;

	@BelongsTo(() => Unit)
	public unit: Unit;

	@HasMany(() => ResultGuide)
	public resultGuide: ResultGuide[];

	@HasMany(() => ResultParticipantOnTrial)
	public resultParticipantOnTrial: ResultParticipantOnTrial[];

	@HasMany(() => TrialOnCompetition)
	public trialOnCompetition: TrialOnCompetition[];

	@HasMany(() => TrialOnGroup)
	public trialOnGroup: TrialOnGroup[];
}
