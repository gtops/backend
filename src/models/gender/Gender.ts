import { AllowNull, Column, DataType, HasMany, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { Participant } from "../participant/Participant";
import { ResultGuide } from "../result-guide/ResultGuide";
import { StandardParent } from "../standard-parent/StandardParent";
import { getOptions } from "../tools/options";

@Table(getOptions("gender"))
export class Gender extends Model<Gender> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	@Column(DataType.INTEGER)
	public gender_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public gender: string;

	@HasMany(() => Participant)
	public participant: Participant[];

	@HasMany(() => ResultGuide)
	public resultGuide: ResultGuide[];

	@HasMany(() => StandardParent)
	public standardParent: StandardParent[];
}