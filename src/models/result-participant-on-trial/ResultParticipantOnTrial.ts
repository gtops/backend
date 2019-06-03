import {
	AllowNull,
	BelongsTo,
	Column,
	DataType,
	ForeignKey,
	Model,
	PrimaryKey,
	Table,
	Unique
} from "sequelize-typescript";
import { AgeCategory } from "../age-category/AgeCategory";
import { ParticipantOnCompetition } from "../participant-on-competition/ParticipantOnCompetition";
import { getOptions } from "../tools/options";
import { Trial } from "../trial/Trial";

@Table(getOptions("result_participant_on_trial"))
export class ResultParticipantOnTrial extends Model<ResultParticipantOnTrial> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public result_participant_on_trial_id: number;

	@ForeignKey(() => ParticipantOnCompetition)
	@Column(DataType.INTEGER)
	public participant_on_competition_id: number;

	@Column(DataType.INTEGER)
	public order_of_participation: number;

	@Column(DataType.INTEGER)
	public primary_result: number;

	@Column(DataType.INTEGER)
	public secondary_result: number;

	@ForeignKey(() => Trial)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@Column(DataType.INTEGER)
	public unique_number: number;

	@BelongsTo(() => Trial)
	public trial: Trial;

	@BelongsTo(() => ParticipantOnCompetition)
	public participantOnCompetition: ParticipantOnCompetition;
}
