import { AllowNull, Column, DataType, HasMany, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { ResultGuide } from "../resultGuide/ResultGuide";
import { ResultParticipantOnTrial } from "../resultParticipantOnTrial/ResultParticipantOnTrial";
import { getOptions } from "../tools/options";
import { TrialOnCompetition } from "../trialOnCompetition/TrialOnCompetition";
import { TrialOnGroup } from "../trialOnGroup/TrialOnGroup";

@Table(getOptions("trial"))
export class Trial extends Model<Trial> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public trial_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public name_of_trial: string;

	@HasMany(() => ResultGuide)
	public resultGuide: ResultGuide[];

	@HasMany(() => ResultParticipantOnTrial)
	public resultParticipantOnTrial: ResultParticipantOnTrial[];

	@HasMany(() => TrialOnCompetition)
	public trialOnCompetition: TrialOnCompetition[];

	@HasMany(() => TrialOnGroup)
	public trialOnGroup: TrialOnGroup[];
}
