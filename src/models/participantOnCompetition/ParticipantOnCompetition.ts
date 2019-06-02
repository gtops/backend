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
import { Participant } from "../participant/Participant";
import { ResultParticipantOnTrial } from "../resultParticipantOnTrial/ResultParticipantOnTrial";
import { getOptions } from "../tools/options";

@Table(getOptions("participant_on_competition"))
export class ParticipantOnCompetition extends Model<ParticipantOnCompetition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public participant_on_competition_id: number;

	@ForeignKey(() => Participant)
	@Column(DataType.INTEGER)
	public participant_id: number;

	@ForeignKey(() => Competition)
	@Column(DataType.INTEGER)
	public competition_id: number;

	@Column(DataType.INTEGER)
	public age_category_id: number;

	@BelongsTo(() => Competition)
	public competition: Competition;

	@BelongsTo(() => Participant)
	public participant: Participant;

	@HasMany(() => ResultParticipantOnTrial)
	public resultParticipantOnTrial: ResultParticipantOnTrial[];
}
