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
import { Competition } from "../competition/Competition";
import { Role } from "../role/Role";
import { getOptions } from "../tools/options";
import { Trial } from "../trial/Trial";

@Table(getOptions("trial_on_competition"))
export class TrialOnCompetition extends Model<TrialOnCompetition> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public trial_on_competition_id: number;

	@ForeignKey(() => Trial)
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public trial_id: number;

	@ForeignKey(() => Competition)
	@AllowNull(false)
	@Unique(false)
	@Column(DataType.INTEGER)
	public competition_id: number;

	@AllowNull(false)
	@Unique(true)
	@Column
	public date_of_start_trial: Date;

	@AllowNull(false)
	@Unique(true)
	@Column(DataType.STRING(50))
	public address: string;

	@BelongsTo(() => Trial)
	public trial: Trial;

	@BelongsTo(() => Competition)
	public role: Competition;
}
