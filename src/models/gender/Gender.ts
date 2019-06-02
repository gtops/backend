import { AllowNull, Column, DataType, HasMany, Model, PrimaryKey, Table, Unique } from "sequelize-typescript";
import { Participant } from "../participant/Participant";
import { ResultGuide } from "../resultGuide/ResultGuide";
import { StandardParent } from "../standardParent/StandardParent";
import { getOptions } from "../tools/options";

@Table(getOptions("gender"))
export class Gender extends Model<Gender> {
	@PrimaryKey
	@AllowNull(false)
	@Unique(true)
	public gender_id: number;

	@AllowNull(false)
	@Unique(false)
	@Column(DataType.STRING(50))
	public gender: string;

	@HasMany(() => Participant)
	public participant;

	@HasMany(() => ResultGuide)
	public resultGuide: ResultGuide[];

	@HasMany(() => StandardParent)
	public standardParent: StandardParent[];
}
