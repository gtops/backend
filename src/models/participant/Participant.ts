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
import { Command } from "../command/Command";
import { Gender } from "../gender/Gender";
import { ParticipantOnCompetition } from "../participant-on-competition/ParticipantOnCompetition";
import { getOptions } from "../tools/options";

@Table(getOptions("participant"))
export class Participant extends Model<Participant> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public participant_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public name: string;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public surname: string;

	@ForeignKey(() => Command)
	@AllowNull(true)
	@Unique(true)
	@Column(DataType.INTEGER)
	public command_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public uid: string;

	@AllowNull(false)
	@Unique(false)
	@Column
	public data_of_birth: Date;

	@AllowNull(true)
	@Unique(false)
	@Column(DataType.BOOLEAN)
	public was_confirmed: boolean;

	@ForeignKey(() => Gender)
	@Column(DataType.INTEGER)
	public gender_id: number;

	@BelongsTo(() => Command)
	public command: Command;

	@BelongsTo(() => Gender)
	public gender: Gender;

	@HasMany(() => ParticipantOnCompetition)
	public participantOnCompetition: ParticipantOnCompetition[];
}
