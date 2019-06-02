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
import { Command } from "../Command/Command";
import { Gender } from "../gender/Gender";
import { ParticipantOnCompetition } from "../participantOnCompetition/ParticipantOnCompetition";
import { getOptions } from "../tools/options";
import { User } from "../user/User";

@Table(getOptions("participant"))
export class Participant extends Model<Participant> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
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
	public command_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public uid: string;

	@AllowNull(false)
	@Unique(false)
	@Column
	public data_of_birth: Date;

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
